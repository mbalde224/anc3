package excel.view;

import excel.viewmodel.SpreadsheetCellViewModel;
import excel.viewmodel.SpreadsheetViewModel;
import javafx.beans.InvalidationListener;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import org.controlsfx.control.spreadsheet.GridBase;
import org.controlsfx.control.spreadsheet.SpreadsheetCell;
import org.controlsfx.control.spreadsheet.SpreadsheetCellType;
import org.controlsfx.control.spreadsheet.SpreadsheetView;

public class MySpreadsheetView extends SpreadsheetView {
    private final SpreadsheetViewModel viewModel;
    private final GridBase grid;
    private SpreadsheetCell selectedCell = null;

    public MySpreadsheetView(SpreadsheetViewModel viewModel) {
        this.viewModel = viewModel;
        this.grid = createGridAndBindings();
        this.setGrid(this.grid);
        configEditLogic();
        layoutSpreadSheet();
    }

    private void layoutSpreadSheet() {
        for (int column = 0; column < grid.getColumnCount(); column++) {
            this.getColumns().get(column).setPrefWidth(150);
        }
    }

    private GridBase createGridAndBindings() {
        GridBase grid = new GridBase(viewModel.getRowCount(), viewModel.getColumnCount());

        for (int row = 0; row < grid.getRowCount(); ++row) {
            final ObservableList<SpreadsheetCell> list = FXCollections.observableArrayList();
            for (int column = 0; column < grid.getColumnCount(); ++column) {
                SpreadsheetCell cell = SpreadsheetCellType.STRING.createCell(row, column, 1, 1, "");
                SpreadsheetCellViewModel cellVM = viewModel.getCellViewModel(row, column);
                cellVM.setCellContentProperty(cell.itemProperty());
                cell.itemProperty().set(viewModel.getCellValue(row, column));
                list.add(cell);
            }
            viewModel.getRows().add(list);
        }
        grid.setRows(viewModel.getRows());
        return grid;
    }

    private void configEditLogic() {

        editingCellProperty().addListener((observable, oldValue, newValue) -> {
            boolean editMode = newValue != null;
            if (editMode) {
                changeEditionMode(true);
            }
        });
        
        this.getSelectionModel().getSelectedCells().addListener((InvalidationListener) il -> {
            if (getSelectionModel().getSelectedCells().isEmpty()) {
                changeEditionMode(false);
                this.selectedCell = null;
            } else {
                var tablePosition = getSelectionModel().getSelectedCells().get(0);
                int row = tablePosition.getRow(), col = tablePosition.getColumn();
                setSelectedCell(row, col);
            }
        });
    }
    
    private void setSelectedCell(int row, int col) {
        changeEditionMode(false);
        this.selectedCell = this.grid.getRows().get(row).get(col);
        viewModel.selectCell(row, col);
    }
    
    private void changeEditionMode(boolean inEdition) {
        if (selectedCell == null) return;
        SpreadsheetCellViewModel cellVM = viewModel.getCellViewModel(selectedCell.getRow(), selectedCell.getColumn());
        cellVM.setEditionMode(inEdition);
    }
}

