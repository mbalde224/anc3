package excel.view;

import excel.viewmodel.CellEditCommand;
import excel.viewmodel.Invoker;
import excel.viewmodel.SpreadsheetViewModel;
import javafx.collections.ListChangeListener;
import javafx.geometry.Insets;
import javafx.scene.control.TablePosition;
import javafx.scene.input.KeyCode;
import javafx.scene.input.KeyEvent;
import javafx.scene.layout.Border;
import javafx.scene.layout.VBox;
import javafx.scene.paint.Color;
import javafx.stage.Stage;

public class MainView extends VBox {
    private  MySpreadsheetView mySpreadSheetView;
    private final HeaderView myHeadView;
    private  SpreadsheetViewModel viewModel;
    FileView fileView;
    private final Invoker invoke;
    
    public MainView(SpreadsheetViewModel viewModel, Stage primarystage, Invoker invoke) {
        this.invoke = invoke;
        this.viewModel = viewModel;
        this.mySpreadSheetView = new MySpreadsheetView(viewModel);
        this.myHeadView = new HeaderView();
        this.fileView = new FileView(viewModel, primarystage, invoke, this);

        this.getChildren().addAll(fileView.menuBar);
        this.getChildren().add(this.myHeadView);

        config();
        makeView();
        beautify();
        setupShortcuts();
    }

    public void setViewModel(SpreadsheetViewModel viewModel) {
        this.viewModel = viewModel;
    }
    public void setMySpreadSheetView(MySpreadsheetView mySpreadSheetView) {
        this.mySpreadSheetView = mySpreadSheetView;
    }
    
    public void config(){
        this.myHeadView.getTextFieldBar().setOnAction(event -> {
            String newText = myHeadView.getTextFieldBar().getText();
            var cell = viewModel.getSelectedCell();
            if (cell != null) {
                String oldText = cell.getExpressionText();
                if (!oldText.equals(newText)) {
                    CellEditCommand command = new CellEditCommand(cell, oldText, newText);
                    fileView.getInvoker().execute(command);
                }
            }
        });
        
        this.mySpreadSheetView.getSelectionModel().getSelectedCells().addListener((ListChangeListener.Change<? extends TablePosition> change) -> {
            if (!change.getList().isEmpty()) {
                TablePosition cell = change.getList().get(0);
                int row = cell.getRow();
                int column = cell.getColumn();
                myHeadView.getTextFieldBar().setText(viewModel.createCell(row, column).getExpressionText());
            }
        });
    }

    private void makeView() {
        this.getChildren().addAll(mySpreadSheetView);
    }

    private void setupShortcuts() {
        this.setOnKeyPressed((KeyEvent event) -> {
            if (event.isControlDown() && event.getCode() == KeyCode.Z) {
                invoke.undo();
                event.consume();
            } else if (event.isControlDown() && event.getCode() == KeyCode.Y) {
                invoke.redo();
                event.consume();
            }
        });
    }
    
    private void beautify() {
        setPadding(new Insets(10));
        setBorder(Border.stroke(Color.BLACK));
        setSpacing(10);
    }
}
