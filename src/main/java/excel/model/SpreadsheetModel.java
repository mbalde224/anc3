package excel.model;

import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import org.controlsfx.control.spreadsheet.SpreadsheetCell;

import java.util.ArrayList;
import java.util.List;
import java.util.regex.Matcher;
import java.util.regex.Pattern;
import java.util.stream.IntStream;

public class SpreadsheetModel {
    private int rows;
    private int columns;
    private final ObservableList<ObservableList<SpreadsheetCellModel>> data = FXCollections.observableArrayList();
    private final ObservableList<ObservableList<SpreadsheetCell>> rowsList = FXCollections.observableArrayList();
    private final ExpressionBuilder eb;

    public SpreadsheetModel(int rows, int columns) {
        this.rows = rows;
        this.columns = columns;
        this.eb = new ExpressionBuilder(this);
        IntStream.range(0, rows).forEach(a -> addNewRow());
    }

    private void addNewRow() {
        ObservableList<SpreadsheetCellModel> newRow = FXCollections.observableArrayList();
        for (int column = 0; column < getColumnCount(); column++) {
            newRow.add(new SpreadsheetCellModel("", eb));
        }
        data.add(newRow);
    }
    
    public ObservableList<ObservableList<SpreadsheetCell>> getRowList() {
        return rowsList;
    }
    
    public int getRowCount() {
        return this.rows;
    }

    public int getColumnCount() {
        return this.columns;
    }

    public SpreadsheetCellModel getCell(int line, int column) {
        System.out.println(line + "line");
        System.out.println(column);
        return data.get(line).get(column);
    }

    private static int columnToNumber(String column) {
        int result = 0;
        for (char c : column.toCharArray()) {
            result = result * 26 + (c - 'A' + 1);
        }
        return result - 1;
    }

    public static int[] cellPos(String s) {
        String coord = s.toUpperCase();

        // Vérifie que le format est bien du type "A1", "AB12", pas test2 etc.
        if (!coord.matches("^[A-Z]{1,3}[1-9][0-9]*$")) {
            return new int[]{-1,-1};//"Invalid cell reference: "
        }

        String column = coord.replaceAll("[0-9]","");
        String row = coord.replaceAll("[A-Z]","");

        int columnIdx = columnToNumber(column);
        int rowIdx = Integer.parseInt(row) - 1;

        return new int[]{rowIdx, columnIdx};
    }
    
    public String cellValue(String token) {
        int[] indices = cellPos(token);
        SpreadsheetCell cellCible = rowsList.get(indices[0]).get(indices[1]);
        return (String) cellCible.getItem();
    }

    public List<Object> getValuesInRange(String startRef, String endRef) {
        List<Object> values = new ArrayList<>();

        int startCol = getColIndex(startRef);
        int endCol = getColIndex(endRef);
        int startRow = getRowIndex(startRef);
        int endRow = getRowIndex(endRef);

        for (int row = Math.min(startRow, endRow); row <= Math.max(startRow, endRow); row++) {
            for (int col = Math.min(startCol, endCol); col <= Math.max(startCol, endCol); col++) {
                SpreadsheetCellModel cell = getCell(row, col);
                values.add(cell.getExpressionValue());
            }
        }
        return values;
    }

    private int getColIndex(String ref) {
        ref = ref.toUpperCase();
        return ref.charAt(0) - 'A';
    }

    private int getRowIndex(String ref) {
        return Integer.parseInt(ref.substring(1)) - 1;
    }

    public String getCellName(SpreadsheetCellModel cell) {
        for (int row = 0; row < data.size(); row++) {
            for (int col = 0; col < data.get(row).size(); col++) {
                if (data.get(row).get(col) == cell) {
                    return cellName(row, col); // exemple : "A3"
                }
            }
        }
        return null;
    }

    private static String cellName(int row, int col) {
        return Character.toString((char) ('A' + col)) + (row + 1);
    }

    public boolean isSelfReferencedInSum(List<String> tokens, String selfName) {
        for (int i = 0; i < tokens.size(); i++) {
            if (tokens.get(i).equalsIgnoreCase("SUM") && i + 4 < tokens.size()) {
                String from = tokens.get(i + 2).toUpperCase();
                String to = tokens.get(i + 4).toUpperCase();

                int[] posFrom = SpreadsheetModel.cellPos(from);
                int[] posTo = SpreadsheetModel.cellPos(to);
                int[] posSelf = SpreadsheetModel.cellPos(selfName);

                int minRow = Math.min(posFrom[0], posTo[0]);
                int maxRow = Math.max(posFrom[0], posTo[0]);
                int minCol = Math.min(posFrom[1], posTo[1]);
                int maxCol = Math.max(posFrom[1], posTo[1]);

                if (posSelf[0] >= minRow && posSelf[0] <= maxRow &&
                        posSelf[1] >= minCol && posSelf[1] <= maxCol) {
                    return true; // circular ref
                }
            }
        }
        return false;
    }
    
    public boolean containsVALEUR(List<String> tokens) {
        return tokens.contains("#VALEUR");
    }

    public boolean containsCellReference(List<String> tokens) {
        for (String token : tokens) {
            if (isCellReference(token)) {
                return true;
            }
        }
        return false;
    }

    private boolean isCellReference(String expression) {
        if (expression.startsWith("=")) {
            expression = expression.substring(1).trim();
        }
        return expression.matches("^[A-Z]+[0-9]+$");
    }

    public boolean containsDivisionByZero(List<String> tokens) {
        for (int i = 0; i < tokens.size(); i++) {
            String token = tokens.get(i);
            if ("/".equals(token) || "%".equals(token)) {
                if (i + 1 < tokens.size()) {
                    String nextToken = tokens.get(i + 1);
                    if ("0".equals(nextToken)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public boolean containsCellReference(String input) {
        String regex = "[A-Z]+[1-9][0-9]*";
        Pattern pattern = Pattern.compile(regex);
        Matcher matcher = pattern.matcher(input);
        return matcher.find();
    }

    public boolean containsJustStringOrNumber(List<String> tokens) {
        if (tokens.size() != 1) {
            return false;
        }
        String tok = tokens.get(0).trim();
        
        boolean isAlpha = tok.matches(".*[A-Za-z].*");
        boolean isNumber = tok.matches("^\\d+([\\.,]\\d+)?$");

        return isAlpha || isNumber;
    }

    public boolean isSumRangeReversed(List<String> tokens) {
        for (int i = 0; i + 5 < tokens.size(); i++) {
            if (tokens.get(i).equalsIgnoreCase("SUM")
                    && tokens.get(i+1).equals("(")
                    && tokens.get(i+3).equals(":")
                    && tokens.get(i+5).equals(")")) {

                String ref1 = tokens.get(i+2).toUpperCase();
                String ref2 = tokens.get(i+4).toUpperCase();
                
                int[] p1 = SpreadsheetModel.cellPos(ref1);
                int[] p2 = SpreadsheetModel.cellPos(ref2);
                
                if (p1[0] > p2[0] || (p1[0] == p2[0] && p1[1] > p2[1])) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }
}
