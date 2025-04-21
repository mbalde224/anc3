package excel.model;

public class SubstractionExpression extends BinaryExpression {
    public SubstractionExpression(Expression left, Expression right) {
        super(left, right);
    }

    @Override
    public double interpret() {
        return getLeft().interpret() - getRight().interpret();
    }
}
