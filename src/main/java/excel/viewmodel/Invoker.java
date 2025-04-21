package excel.viewmodel;

import javafx.beans.binding.Bindings;
import javafx.beans.binding.BooleanBinding;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;

public class Invoker {
    private final ObservableList<CellEditCommand> undoStack = FXCollections.observableArrayList();
    private final ObservableList<CellEditCommand> redoStack = FXCollections.observableArrayList();
    private final BooleanBinding isUndoEmpty;
    private final BooleanBinding isRedoEmpty;

    public Invoker() {
        this.isUndoEmpty = Bindings.isEmpty(undoStack);
        this.isRedoEmpty = Bindings.isEmpty(redoStack);
    }

    public void execute(CellEditCommand command) {
        command.redo();
        undoStack.add(command);
        redoStack.clear();
    }

    public void undo() {
        if (!undoStack.isEmpty()) {
            CellEditCommand command = undoStack.remove(undoStack.size() - 1);
            command.undo();
            redoStack.add(command);
        }
    }

    public void redo() {
        if (!redoStack.isEmpty()) {
            CellEditCommand command = redoStack.remove(redoStack.size() - 1);
            command.redo();
            undoStack.add(command);
        }
    }

    public BooleanBinding undoDisabledProperty() {
        return isUndoEmpty;
    }

    public BooleanBinding redoDisabledProperty() {
        return isRedoEmpty;
    }
}
