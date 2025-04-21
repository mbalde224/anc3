package excel.viewmodel;

import excel.model.SpreadsheetCellModel;
import javafx.beans.property.BooleanProperty;
import javafx.beans.property.ObjectProperty;
import javafx.beans.property.SimpleBooleanProperty;
import javafx.beans.value.ChangeListener;

public class SpreadsheetCellViewModel  {
    private final SpreadsheetCellModel model;
    private final BooleanProperty editionMode = new SimpleBooleanProperty(false);
    private ObjectProperty<Object> cellContentProperty;
    private final Invoker invoke;
    
    public SpreadsheetCellViewModel(SpreadsheetCellModel model, Invoker invoke) {
        this.model  = model;
        this.invoke = invoke;
    }

    private static String getStringFromObject(Object value) {
        return value == null ? "" : value.toString();
    }
    
    public void setCellContentProperty(ObjectProperty<Object> cellContentProperty) {
        this.cellContentProperty = cellContentProperty;
        
        ChangeListener<Object> valueListener = (obs, ov, nv) -> {
            if (!isEditionMode()) {
                this.cellContentProperty.set(getStringFromObject(nv));
            }
        };
        
        model.expressionValueProperty().addListener(valueListener);
        
        ChangeListener<Object> expressionSetterListener = (obs, ov, nv) -> {
            if (isEditionMode()) {
                CellEditCommand command = new CellEditCommand(model, model.getExpressionText(), getStringFromObject(nv) );
                invoke.execute(command);
                model.setExpressionText(getStringFromObject(nv));
            }
        };
       
        this.cellContentProperty.addListener(expressionSetterListener);

    }

    public String getCellValue() {
        return model.getExpressionValue();
    }

    public SpreadsheetCellModel getModel() {
        return model;
    }

    private boolean isEditionMode() {
        return editionMode.get();
    }

    public void setEditionMode(boolean editionMode) {
        this.editionMode.set(editionMode);
        if (cellContentProperty != null) {
            cellContentProperty.set(editionMode ? model.getExpressionText() : getCellValue());
        }
    }
}