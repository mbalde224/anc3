package excel.model;

public class AdditionExpression extends BinaryExpression {

    public AdditionExpression(Expression left, Expression right) {
        super(left, right);
    }

    @Override
    public double interpret() {
        return getLeft().interpret() + getRight().interpret() ;
    }
}
