package excel.view;

import javafx.scene.control.TextField;
import javafx.scene.layout.VBox;

public class HeaderView extends VBox{
    private final TextField textFieldBar;

    public HeaderView(){
        this.textFieldBar = new TextField();

        TextFieldBar();
        getChildren().add(this.textFieldBar);
    }
    
    private void TextFieldBar(){
        textFieldBar.setPromptText("Entrez une valeur ou une formule");
    }
    
    public TextField getTextFieldBar(){
        return this.textFieldBar;
    }
}
