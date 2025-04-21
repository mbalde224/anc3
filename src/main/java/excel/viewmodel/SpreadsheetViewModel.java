package excel.viewmodel;

import excel.model.SpreadsheetCellModel;
import excel.model.SpreadsheetModel;
import javafx.beans.property.ObjectProperty;
import javafx.beans.property.SimpleObjectProperty;
import javafx.collections.ObservableList;
import org.controlsfx.control.spreadsheet.SpreadsheetCell;

import java.util.ArrayList;
import java.util.List;

public class SpreadsheetViewModel {
    private final int NB_ROW, NB_COL;
    private final List<SpreadsheetCellViewModel> cellVMs = new ArrayList<>();
    private final ObjectProperty<SpreadsheetCellModel> selectedCell = new SimpleObjectProperty<>();
    private final SpreadsheetModel model;
    private final Invoker invoke;

    public SpreadsheetViewModel(SpreadsheetModel model, Invoker invoke) {
        this.model = model;
        this.invoke = invoke;
        this.NB_ROW = model.getRowCount();
        this.NB_COL = model.getColumnCount();

        for (int i = 0; i < NB_ROW; i++) {
            for (int j = 0; j < NB_COL; j++) {
                cellVMs.add(new SpreadsheetCellViewModel(model.getCell(i, j),this.invoke));
            }
        }
    }

    public SpreadsheetCellModel createCell(int row, int column) {
        SpreadsheetCellModel seCell = this.getSpreadsheetModel().getCell(row, column);
        return seCell;
    }

    public SpreadsheetCellViewModel getCellViewModel(int row, int col) {
        return cellVMs.get(row * NB_COL + col);
    }

    public int getRowCount() {
        return NB_ROW;
    }

    public int getColumnCount() {
        return NB_COL;
    }

    public String getCellValue(int row, int col) {
        return getCellViewModel(row, col).getCellValue();
    }

    public void selectCell(int row, int col) {
        selectedCell.set(model.getCell(row, col));
    }

    public SpreadsheetModel getSpreadsheetModel() {
        return this.model;
    }
    
    public ObservableList<ObservableList<SpreadsheetCell>> getRows() {
        return model.getRowList();
    }
    
    public SpreadsheetCellModel getSelectedCell() {
        return selectedCell.get();
    }
}