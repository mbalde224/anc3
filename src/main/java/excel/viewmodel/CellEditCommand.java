package excel.viewmodel;

import excel.model.SpreadsheetCellModel;

public class CellEditCommand {
    private final SpreadsheetCellModel cell;
    private final String oldValue;
    private final String newValue;

    public CellEditCommand(SpreadsheetCellModel cell, String oldValue, String newValue) {
        this.cell = cell;
        this.oldValue = oldValue;
        this.newValue = newValue;
    }

    public void undo() {
        cell.setExpressionText(oldValue);
    }

    public void redo() {
        cell.setExpressionText(newValue);
    }
}

