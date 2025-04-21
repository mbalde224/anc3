package excel.model;

import javafx.beans.Observable;
import javafx.beans.binding.Bindings;
import javafx.beans.binding.StringBinding;
import javafx.beans.property.ReadOnlyStringProperty;
import javafx.beans.property.ReadOnlyStringWrapper;
import javafx.beans.property.SimpleStringProperty;
import javafx.beans.property.StringProperty;

import java.util.ArrayList;
import java.util.List;
import java.util.Objects;
import java.util.regex.Matcher;
import java.util.regex.Pattern;
import java.util.stream.Stream;

public class SpreadsheetCellModel {
    private final StringProperty expressionText = new SimpleStringProperty("");
    private final ReadOnlyStringWrapper expressionValue = new ReadOnlyStringWrapper("");
    private final List<SpreadsheetCellModel> dependencies = new ArrayList<>();
    private Observable[] dependencyObservables = new Observable[0];
    private final ExpressionBuilder eb;
    
    public SpreadsheetCellModel(String text, ExpressionBuilder eb) {
        this.eb = eb;
        this.expressionText.set(text);
        
        updateDependencies();
        rebuildExpressionBinding();
    }

    private String calculate() {
        String expr = getExpressionText();
        if (expr == null || expr.isEmpty()) {
            return "";
        }
        return expr.charAt(0) == '=' ? eb.toString(expr, this) : eb.arrondirSiNecessaire(expr);
    }

    public String getExpressionText() {
        return expressionText.get();
    }

    public void setExpressionText(String val) {
        if (!Objects.equals(expressionText.get(), val)) {
            expressionText.set(val);
            updateDependencies();
            rebuildExpressionBinding();
        }
    }

    public ReadOnlyStringProperty expressionValueProperty() {
        return expressionValue.getReadOnlyProperty();
    }

    public String getExpressionValue() {
        return expressionValue.get();
    }

    private void rebuildExpressionBinding() {
        if (expressionValue.isBound()) {
            expressionValue.unbind();
        }

        StringBinding calcBinding = Bindings.createStringBinding(
                this::calculate,
                dependencyObservables
        );

        expressionValue.bind(calcBinding);
    }

    private void updateDependencies() {
        dependencies.clear();

        String expr = getExpressionText().toUpperCase();
        
        Matcher rangeMatcher = Pattern.compile("([A-Z]+[1-9][0-9]*):([A-Z]+[1-9][0-9]*)").matcher(expr);
        while (rangeMatcher.find()) {
            String refStart = rangeMatcher.group(1);
            String refEnd = rangeMatcher.group(2);
            int[] start = SpreadsheetModel.cellPos(refStart);
            int[] end = SpreadsheetModel.cellPos(refEnd);

            int minRow = Math.min(start[0], end[0]);
            int maxRow = Math.max(start[0], end[0]);
            int minCol = Math.min(start[1], end[1]);
            int maxCol = Math.max(start[1], end[1]);

            for (int row = minRow; row <= maxRow; row++) {
                for (int col = minCol; col <= maxCol; col++) {
                    SpreadsheetCellModel target = eb.getModel().getCell(row, col);
                    if (!dependencies.contains(target)) {
                        dependencies.add(target);
                    }
                }
            }
        }
        
        Matcher singleMatcher = Pattern.compile("([A-Z]+[1-9][0-9]*)").matcher(expr);
        while (singleMatcher.find()) {
            String ref = singleMatcher.group(1);
            boolean alreadyInRange = expr.matches(".*" + ref + ":.*") || expr.matches(".*:" + ref + ".*");
            if (!alreadyInRange) {
                int[] pos = SpreadsheetModel.cellPos(ref);
                if (pos[0] != -1 && pos[1] != -1){
                    SpreadsheetCellModel target = eb.getModel().getCell(pos[0], pos[1]);
                    if (!dependencies.contains(target)) {
                        dependencies.add(target);
                    }
                }

            }
        }
        
        Stream<Observable> textStream = Stream.of(expressionText);
        Stream<Observable> depsStream = dependencies.stream()
                .map(dep -> dep.expressionValueProperty());

        dependencyObservables = Stream.concat(textStream, depsStream)
                .toArray(Observable[]::new);
    }
}