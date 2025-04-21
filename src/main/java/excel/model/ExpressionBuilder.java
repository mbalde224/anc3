package excel.model;
import java.util.*;

public class ExpressionBuilder {
    private final SpreadsheetModel model;
    private SpreadsheetCellModel self;

    public ExpressionBuilder(SpreadsheetModel model) {
        this.model = model;
    }

    public SpreadsheetModel getModel() {
        return model;
    }

    private void setSelf(SpreadsheetCellModel self) {
        this.self = self;
    }

    public Object build(String strExpr) {
        if (strExpr == null || strExpr.trim().isEmpty()) {
            return null;
        } else try {
            strExpr = strExpr.trim();
            String cellule_actuelle = model.getCellName(self);
            List<String> tokens = tokenize(strExpr.substring(1));
            
            if (tokens.isEmpty()) {
                return null;
            } else if (model.containsVALEUR(tokens)) {
                return "#VALEUR";
            } else if (model.containsCellReference(tokens)) {
                return "#CIRCULAR_REF";
            } else if (model.containsDivisionByZero(tokens)){
                return "#VALEUR";
            } else if (model.isSelfReferencedInSum(tokens, cellule_actuelle)) {
                return "#CIRCULAR_REF";
            } else if (model.isSumRangeReversed(tokens)) {
                return "#SYNTAX_ERROR";
            } else if (model.containsJustStringOrNumber(tokens)) {
                return tokens.get(0);
            } else  if (model.containsCellReference(strExpr)) {
                try {
                    return containsOp2(tokens) ? makeBoolean(tokens) : buildExpression(tokens);
                }
                catch (Exception e) {
                    return "#VALEUR";
                }
            } else return containsOp2(tokens) ? makeBoolean(tokens) : buildExpression(tokens);

        } catch (Exception e) {
            return "SYNTAX_ERROR";
        }
    }

    private Expression buildExpression(List<String> tokens) {
        int idxOp = findLastOperator(tokens);

        if (idxOp != -1) {
            String op = tokens.get(idxOp);

            List<String> leftTokens = new ArrayList<>(tokens.subList(0, idxOp));
            List<String> rightTokens = new ArrayList<>(tokens.subList(idxOp + 1, tokens.size()));

            Expression left = buildExpression(leftTokens);
            Expression right = buildExpression(rightTokens);

            return makeOpExpression(op, left, right);
        }

        if (tokens.size() == 1) {
            String token = tokens.get(0);
            
            if (token.startsWith("=")) {
                Object result = build(token);
                if (result instanceof Expression) {
                    return (Expression) result;
                } else if (result instanceof Number) {
                    return new NumberExpression(((Number) result).doubleValue());
                } else if (result instanceof String) {
                    throw new RuntimeException("Invalid expression value: " + result);
                }
            }
            try {
                return new NumberExpression(Double.parseDouble(token));
            } catch (NumberFormatException e) {
                throw new RuntimeException("Invalid numeric token: " + token, e);
            }
        }
        return buildSum(tokens);
    }

    private Expression buildSum(List<String> tokens) {
        String ref1 = tokens.get(2).toUpperCase();
        String ref2 = tokens.get(4).toUpperCase();
        
        if (ref1.charAt(0) == ref2.charAt(0)) {
            double sum = 0.0;
            List<Object> values = getValuesFromRange(ref1, ref2);

            for (Object val : values) {
                sum += getNumericValue(val);
            }

            return new NumberExpression(sum);
        } else {
            StringBuilder ref3 = new StringBuilder();
            ref3.append(ref1.charAt(0));
            for (int i = 1; i < ref2.length(); ++i) {
                ref3.append(ref2.charAt(i));
            }

            StringBuilder ref4 = new StringBuilder();
            ref4.append(ref2.charAt(0));
            for (int i = 1; i < ref1.length(); ++i) {
                ref4.append(ref1.charAt(i));
            }

            double sum1 = 0.0;
            for (Object val : getValuesFromRange(ref1, ref3.toString())) {
                sum1 += getNumericValue(val);
            }
            
            double sum2 = 0.0;
            for (Object val : getValuesFromRange(ref4.toString(), ref2)) {
                sum2 += getNumericValue(val);
            }
            
            return new NumberExpression(sum1 + sum2);
        }
    }

    private List<Object> getValuesFromRange(String startRef, String endRef) {
        return model.getValuesInRange(startRef, endRef); // ou adapte selon ta logique
    }

    private double getNumericValue(Object val) {
        if (val == null) return 0.0;

        if (val instanceof Number) {
            return ((Number) val).doubleValue();
        }

        if (val instanceof String str) {
            str = str.trim();
            if (str.startsWith("=")) {
                Object exprResult = build(str);
                return getNumericValue(exprResult);
            }

            try {
                return Double.parseDouble(str);
            } catch (NumberFormatException e) {
                return 0.0;
            }
        }

        if (val instanceof Expression expr) {
            Object evaluated = expr.interpret();
            return getNumericValue(evaluated);
        }

        return 0.0;
    }

    private boolean makeBoolean(List<String> tokens) {
        int idxOp = findLastOperator2(tokens);

        if (idxOp != -1) {
            String op = tokens.get(idxOp);

            List<String> leftTokens = new ArrayList<>(tokens.subList(0, idxOp));
            List<String> rightTokens = new ArrayList<>(tokens.subList(idxOp + 1, tokens.size()));

            Boolean left = makeBoolean(leftTokens);
            Boolean right = makeBoolean(rightTokens);

            return makeOpBoolean(op, left, right);

        } else if (tokens.contains("NOT")) {
            List<String> list = removeNOT(tokens);
            if (list.size() == 1) {
                return !Boolean.parseBoolean(list.get(0));
            } return !buildBool(list);
        }
        else if (tokens.size() == 1) {
            if (isBoolean(tokens.get(0))) {
                return Boolean.parseBoolean(tokens.get(0));
            } else throw new IllegalArgumentException("Invalid boolean expression");
        }
        else {
            return buildBool(tokens);
        }
    }

    private boolean isBoolean(String input) {
        return "true".equalsIgnoreCase(input) || "false".equalsIgnoreCase(input);
    }

    private List<String> removeNOT(List<String> tokens) {
        List<String> list = new ArrayList<>(tokens.subList(1, tokens.size()));
        return list;
    }

    private boolean makeOpBoolean(String op, Boolean left, Boolean right) {
        return "or".equalsIgnoreCase(op) ? left || right : left && right;
    }

    private boolean buildBool(List<String> tokens) {
        if (tokens.size() < 3) {
            throw new IllegalArgumentException("Invalid boolean expression");
        }

        String op = containsOp2String(tokens);
        if (op == null) {
            throw new IllegalArgumentException("Missing comparison operator");
        }

        int opIndex = tokens.indexOf(op);
        if (opIndex == -1) {
            throw new IllegalArgumentException("Operator not found");
        }

        List<String> leftTokens = tokens.subList(0, opIndex);
        List<String> rightTokens = tokens.subList(opIndex + 1, tokens.size());

        if (leftTokens.isEmpty() || rightTokens.isEmpty()) {
            throw new IllegalArgumentException("Missing operands for comparison");
        }

        double left = buildExpression(leftTokens).interpret();
        double right = buildExpression(rightTokens).interpret();

        return buildBoolean(left, right, op);
    }

    private boolean buildBoolean(double left, double right, String op) {
        return switch (op) {
            case "<" -> left < right;
            case "<=" -> left <= right;
            case ">" -> left > right;
            case ">=" -> left >= right;
            case "=" -> left == right;
            case "!=" -> left != right;
            default -> false;
        };
    }

    private int findLastOperator(List<String> tokens) {
        int result = -1;
        for (int i = tokens.size() - 1; i >= 0; i--) {
            String token = tokens.get(i);
            if (priority(tokens) && isOpPrio2(token)) {
                result = i;
            } else if (isOpPrio1(token))
                result = i;
        }
        return result;
    }

    private int findLastOperator2(List<String> tokens) {
        boolean containsAND = tokens.contains("AND");
        boolean containsOR = tokens.contains("OR");
        int result = -1;

        if (containsAND || containsOR) {
            for (int i = tokens.size() - 1; i >= 0; i--) {
                String token = tokens.get(i);
                if (containsOR && result == -1) {
                    result = token.equals("OR") ? i : -1;
                } else if (containsAND && result == -1) {
                    result = token.equals("AND") ? i : -1;
                }
            }
        }
        return result;
    }

    private boolean priority(List<String> tokens) {
        return (tokens.contains("*") || tokens.contains("/")) &&
                (!tokens.contains("+") && (!tokens.contains("-")));
    }

    private List<String> tokenize(String strExpr) {
        List<String> tokens = new ArrayList<>();
        StringBuilder number = new StringBuilder();
        boolean isAlphabetic = false;

        List<String> keywords = Arrays.asList("AND", "OR", "NOT", "TRUE", "FALSE", "SUM");

        for (int i = 0; i < strExpr.length(); ++i) {
            char c = strExpr.charAt(i);

            String matchedKeyword = matchKeyword(strExpr, i, keywords);
            if (matchedKeyword != null) {
                addToken(tokens, number, isAlphabetic);
                isAlphabetic = false;
                tokens.add(matchedKeyword);
                i += matchedKeyword.length() - 1;
                number.setLength(0);
                continue;
            }

            if (c == '(') {
                tokens.add("(");

                int closingParenthesisIndex = findClosingParenthesis(strExpr, i);

                if (closingParenthesisIndex != -1) {
                    String insideParentheses = strExpr.substring(i + 1, closingParenthesisIndex);
                    if (insideParentheses.contains(":")) {
                        String[] refs = insideParentheses.split(":");
                        addToken(tokens, number, isAlphabetic);
                        isAlphabetic = false;
                        tokens.add(refs[0].trim().toLowerCase());
                        tokens.add(":");
                        tokens.add(refs[1].trim().toLowerCase());
                    }

                    tokens.add(")");
                }

                i = closingParenthesisIndex;
                continue;
            }

            if (Character.isAlphabetic(c)) {
                number.append(c);
                isAlphabetic = true;
            }

            else if (Character.isDigit(c)) {
                number.append(c);
            }

            else if (isOp2(c)) {
                addToken(tokens, number, isAlphabetic);
                isAlphabetic = false;
                number.setLength(0);
                if (i + 1 < strExpr.length() && strExpr.charAt(i + 1) == '=') {
                    number.append(c + "=");
                    addToken(tokens, number, isAlphabetic);
                    number.setLength(0);
                    i++;
                } else {
                    number.append(c);
                    addToken(tokens, number, isAlphabetic);
                    number.setLength(0);
                }
            }

            else if ((isOp(c)) && (!number.toString().isEmpty()) || (isOp(c)) && (Objects.equals(tokens.get(tokens.size() - 1), ")"))) {
                addToken(tokens, number, isAlphabetic);
                isAlphabetic = false;
                tokens.add(String.valueOf(c));
                number.setLength(0);
            }
        }

        addToken(tokens, number, isAlphabetic);
        return tokens;
    }

    private void addToken(List<String> tokens, StringBuilder number, boolean isAlphabetic) {
        if (isAlphabetic) {
            String cellvalue = model.cellValue((number.toString()));
            if (cellvalue.isEmpty()) {
                tokens.add("#VALEUR");
            } else tokens.add(model.cellValue(number.toString()));
        } else if (!number.isEmpty()) {
            tokens.add(number.toString());
        }
        number.setLength(0);
    }

    private String matchKeyword(String strExpr, int index, List<String> keywords) {
        String result = null;
        for (String keyword : keywords) {
            if (index + keyword.length() <= strExpr.length() && strExpr.substring(index, index + keyword.length()).equalsIgnoreCase(keyword)) {
                result = keyword;
            }
        }
        return result;
    }

    private int findClosingParenthesis(String strExpr, int openIndex) {
        int balance = 1;
        for (int i = openIndex + 1; i < strExpr.length(); i++) {
            if (strExpr.charAt(i) == '(') {
                balance++;
            } else if (strExpr.charAt(i) == ')') {
                balance--;
                if (balance == 0) {
                    return i;
                }
            }
        }
        return -1;
    }

    private boolean isOpPrio1(char c) {
        return c == '+' || c == '-';
    }

    private boolean isOpPrio1(String s) {return isOpPrio1(s.charAt(0));}

    private boolean isOpPrio2(char c) {
        return c == '*' || c == '/';
    }

    private boolean isOpPrio2(String s) {return isOpPrio2(s.charAt(0));}

    private boolean isOp(char c) {
        return c == '+' || c == '-' || c == '*' || c == '/';
    }

    private boolean isOp2(char c) {
        return c == '>' || c == '<' || c == '=' || c == '!';
    }

    private boolean containsOp2(List<String> tokens) {
        return tokens.contains("<") || tokens.contains("<=") || tokens.contains(">") || tokens.contains(">=") || tokens.contains("=") || tokens.contains("!=") ||
                tokens.contains("TRUE") || tokens.contains("FALSE");
    }

    private String containsOp2String(List<String> tokens) {
        String op;
        if (tokens.contains("<"))
            op =  "<";
        else if (tokens.contains("<="))
            op = "<=";
        else if (tokens.contains(">"))
            op = ">";
        else if (tokens.contains(">="))
            op = ">=";
        else if (tokens.contains("="))
            op = "=";
        else if (tokens.contains("!="))
            op = "!=";
        else op = null;

        return op;
    }

    private BinaryExpression makeOpExpression(String tokenOp, Expression left, Expression right) {
        return switch (tokenOp) {
            case "+" -> new AdditionExpression(left, right);
            case "-" -> new SubstractionExpression(left, right);
            case "*" -> new MultiplyExpression(left, right);
            case "/" -> new DivideExpression(left, right);
            default -> null;
        };
    }

    public String toString(String s, SpreadsheetCellModel self) {
        if (s == null || s.trim().isEmpty()) {
            return "";
        }
        setSelf(self);
        Object result = build(s);

        if (result instanceof String) {
            String strResult = (String) result;
            if ("SYNTAX_ERROR".equals(strResult) || "#VALEUR".equals(strResult) || "#CIRCULAR_REF".equals(strResult)) {
                return strResult;
            } else return strResult;

        } else if (result instanceof Expression) {
            double value = ((Expression)result).interpret();
            return value == (int)value ? String.valueOf((int)value) : arrondirSiNecessaire(String.valueOf(value));

        } else if (result instanceof Boolean) {
            return ((Boolean)result) ? "true" : "false";
        } else return "";
    }
    
    public String arrondirSiNecessaire(String valeur) {
        if (valeur.contains(",")) {
            String[] parties = valeur.split(",");
            if (parties.length == 2 && parties[1].length() > 2) {
                double nombre = Double.parseDouble(valeur.replace(",", "."));
                return String.format("%.2f", nombre).replace(".", ",");
            }
        } else if (valeur.contains(".")) {
            String[] parties = valeur.split("\\.");
            if (parties.length == 2 && parties[1].length() > 2) {
                double nombre = Double.parseDouble(valeur);
                return String.format("%.2f", nombre);
            }
        }
        return valeur;
    }
}