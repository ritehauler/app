import React, { PureComponent } from "react";
import { connect } from "react-redux";
import { View, Text, StyleSheet } from "react-native";
import { Actions } from "react-native-router-flux";
import { ApplicationStyles, Colors, Metrics } from "../../theme";
import { RetryButton } from "../../appComponents";

// redux imports
import { success as assignedOrdersSuccess } from "../../actions/AssignedOrders";

class OrderAutoDeclined extends PureComponent {
  constructor(props) {
    super(props);
    this.cbOnPress = this.cbOnPress.bind(this);
  }

  removeTopMostAssignedOrder(data) {
    let tempData = _.cloneDeep(data);
    tempData.splice(0, 1);
    return tempData;
  }

  cbOnPress() {
    if (this.props.assignedOrders.data.length > 1) {
      this.props.assignedOrdersSuccess(this.props.assignedOrders.data);
      setTimeout(() => Actions.pop(), 500);
    } else {
      Actions.pop();
    }
  }

  render() {
    return (
      <View style={styles.container}>
        <Text>Current order has been auto declined</Text>
        <RetryButton
          text={this.props.assignedOrders.data.length > 1 ? "Next" : "Back"}
          onPress={this.cbOnPress}
        />
      </View>
    );
  }
}

const actions = { assignedOrdersSuccess };
const mapStateToProps = ({ assignedOrders }) => ({ assignedOrders });

export default connect(mapStateToProps, actions)(OrderAutoDeclined);

const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: Colors.background.login
  }
});
