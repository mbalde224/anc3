package excel.model;

public class NumberExpression extends Expression {

    private final double value;

    public NumberExpression(double value) {
        this.value = value;
    }

    @Override
    public double interpret() {
        return value;
    }
}
