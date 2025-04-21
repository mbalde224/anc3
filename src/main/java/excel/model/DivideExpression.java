package excel.model;

public class DivideExpression extends BinaryExpression {
    public DivideExpression(Expression left, Expression right) {
        super(left, right);
    }

    @Override
    public double interpret() {
        return getLeft().interpret() / getRight().interpret();
    }

}