package excel.model;

public class MultiplyExpression extends BinaryExpression {

    public MultiplyExpression(Expression left, Expression right) {super(left,right); }

    @Override
    public double interpret() {
        return getLeft().interpret() * getRight().interpret();
    }
}
