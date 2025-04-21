package excel;
import com.tangorabox.componentinspector.fx.FXComponentInspectorHandler;
import excel.model.SpreadsheetModel;
import excel.view.MainView;
import excel.viewmodel.Invoker;
import excel.viewmodel.SpreadsheetViewModel;
import javafx.application.Application;
import javafx.scene.Scene;
import javafx.stage.Stage;

public class App extends Application {

    @Override
    public void start(Stage primaryStage) {
        Invoker invoke = new Invoker();
        SpreadsheetModel model = new SpreadsheetModel(10, 5);
        SpreadsheetViewModel viewModel = new SpreadsheetViewModel(model, invoke);
        Stage primarystage = primaryStage;
        MainView root = new MainView(viewModel, primarystage,invoke);

        FXComponentInspectorHandler.handleAll();

        Scene scene = new Scene(root, 633, 315);
        primarystage.setTitle("Spreadsheet");
        primarystage.setScene(scene);
        primarystage.show();
    }

    public static void main(String[] args) {
        launch(args);
    }
}