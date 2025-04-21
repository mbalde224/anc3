package excel.view;

import excel.model.SpreadsheetCellModel;
import excel.viewmodel.Invoker;
import excel.model.SpreadsheetModel;
import excel.viewmodel.SpreadsheetViewModel;
import javafx.collections.ObservableList;
import javafx.stage.Stage;

import java.io.*;
import javafx.stage.FileChooser;
import javafx.scene.control.*;

import org.controlsfx.control.spreadsheet.SpreadsheetCell;

public class FileView {
    SpreadsheetViewModel spreadsheetViewModel;
    MenuBar menuBar;
    MainView mainView;
    private final MenuItem undoItem;
    private final MenuItem redoItem;
    private final Invoker invoker;
    
    FileView (SpreadsheetViewModel spreadsheetViewModel_, Stage primarystage, Invoker invoke, MainView mainView_){
        menuBar = new MenuBar();
        Menu fileMenu = new Menu("File");
        MenuItem openItem = new MenuItem("Open");
        MenuItem saveItem = new MenuItem("Save");
        Menu editMenu = new Menu("Edit");
        undoItem = new MenuItem("Undo");
        redoItem = new MenuItem("Redo");
        this.invoker = invoke;
        openItem.setOnAction(e -> openFile(primarystage));
        saveItem.setOnAction(e -> saveFile(primarystage));
        getActions();
        fileMenu.getItems().addAll(openItem, saveItem);
        editMenu.getItems().addAll(undoItem,redoItem);
        menuBar.getMenus().add(fileMenu);
        menuBar.getMenus().add(editMenu);
        spreadsheetViewModel = spreadsheetViewModel_;
        mainView = mainView_;
        updateButtons(invoker);
    }
    
    private void saveFile(Stage stage) {
        FileChooser fileChooser = new FileChooser();
        fileChooser.setTitle("Save Spreadsheet");
        fileChooser.getExtensionFilters().add(new FileChooser.ExtensionFilter("E4E Files", "*.e4e"));
        File file = fileChooser.showSaveDialog(stage);
        if (file != null) {
            try (BufferedWriter writer = new BufferedWriter(new FileWriter(file))) {
                writer.write(spreadsheetViewModel.getRows().size() + "," + spreadsheetViewModel.getColumnCount());
                writer.newLine();
                for (int row = 0; row < spreadsheetViewModel.getRows().size(); row++) {
                    ObservableList<SpreadsheetCell> rowData = spreadsheetViewModel.getRows().get(row);
                    for (int col = 0; col < rowData.size(); col++) {
                        SpreadsheetCellModel seCell = spreadsheetViewModel.getSpreadsheetModel().getCell(row,col);
                        if (seCell != null && !seCell.getExpressionText().isEmpty()) {
                            writer.write(row + "," + col + ";" + seCell.getExpressionText());
                            writer.newLine();
                        }
                    }
                }
                System.out.println("Spreadsheet saved to " + file.getAbsolutePath());
            } catch (IOException e) {
                System.err.println("Error saving file: " + e.getMessage());
            }
        }
    }
    
    private void openFile(Stage stage) {
        FileChooser fileChooser = new FileChooser();
        fileChooser.setTitle("Open Spreadsheet");
        fileChooser.getExtensionFilters().add(new FileChooser.ExtensionFilter("E4E Files", "*.e4e"));
        File file = fileChooser.showOpenDialog(stage);
        if (file != null) {

            try (BufferedReader reader = new BufferedReader(new FileReader(file))) {
                String firstLine = reader.readLine();
                if (firstLine == null) return;
                String line;
                for (int row = 0; row < spreadsheetViewModel.getRows().size(); row++) {
                    ObservableList<SpreadsheetCell> rowData = spreadsheetViewModel.getRows().get(row);
                    for (int col = 0; col < rowData.size(); col++) {
                        SpreadsheetCellModel seCell = spreadsheetViewModel.getSpreadsheetModel().getCell(row,col);
                        if (seCell != null ) {
                            seCell.setExpressionText("");
                        }
                    }
                }
                String[] dimensions = firstLine.split(",");
                int rowCount = Integer.parseInt(dimensions[0]);
                int cols = Integer.parseInt(dimensions[1]);
                mainView.getChildren().remove(2);
                SpreadsheetModel model = new SpreadsheetModel(rowCount, cols);
                spreadsheetViewModel= new SpreadsheetViewModel(model, invoker);
                mainView.setViewModel(spreadsheetViewModel);
                MySpreadsheetView mySpreadSheetView = new MySpreadsheetView(spreadsheetViewModel);
                mainView.setMySpreadSheetView(mySpreadSheetView);
                mainView.getChildren().addAll(mySpreadSheetView);
                mainView.config();
                while ((line = reader.readLine()) != null) {
                    String[] parts = line.split(";");
                    if (parts.length == 2) {
                        String[] position = parts[0].split(",");
                        int row = Integer.parseInt(position[0]);
                        int col = Integer.parseInt(position[1]);
                        System.out.println("rows et col");
                        System.out.println(row);
                        System.out.println(col);
                        SpreadsheetCellModel seCell = spreadsheetViewModel.getSpreadsheetModel().getCell(row,col);
                        System.out.println(parts[1] + " valeur");
                        seCell.setExpressionText(parts[1]);
                    }else {
                        String[] position = parts[0].split(",");
                        int row = Integer.parseInt(position[0]);
                        int col = Integer.parseInt(position[1]);
                        SpreadsheetCellModel seCell = spreadsheetViewModel.getSpreadsheetModel().getCell(row,col);
                        seCell.setExpressionText("");
                    }
                }
                System.out.println("Spreadsheet loaded from " + file.getAbsolutePath());
            } catch (IOException e) {
                System.err.println("Error loading file: " + e.getMessage());
            }
        }
    }
    
    private void getActions(){
        undoItem.setOnAction(e -> {
            invoker.undo();
            updateButtons(invoker);
        });

        redoItem.setOnAction(e -> {
            invoker.redo();
            updateButtons(invoker);
        });
    }

    private void updateButtons(Invoker invoker) {
        undoItem.disableProperty().bind(invoker.undoDisabledProperty());
        redoItem.disableProperty().bind(invoker.redoDisabledProperty());
    }

    public Invoker getInvoker() {
        return invoker;
    }
    
}