import React, { Component } from "react";
import { connect } from "react-redux";
import { View, FlatList, Text, Image } from "react-native";
import { Actions } from "react-native-router-flux";
import { ButtonView } from "../../../components";
import { Colors, Metrics, ApplicationStyles, Images } from "../../../theme";

class OrderItem extends Component<{}> {
  render() {
    const {
      time = "",
      status = "",
      location = "",
      payment = { id: "", method: "", amount: "" },
      distance = "",
      entityID
    } = this.props.data;

    return (
      <ButtonView
        style={styles.container}
        onPress={() => {
          this.props.cbOnItemPress(entityID, payment.id);
        }}
      >
        <View
          style={[
            styles.horizontalWrapper,
            { marginBottom: Metrics.smallMargin, alignItems: "flex-start" }
          ]}
        >
          <Text style={[ApplicationStyles.tBold16, { flex: 1 }]}>{time}</Text>
          <Text style={[ApplicationStyles.re13Gray]}>{distance}</Text>
        </View>

        <Text style={ApplicationStyles.re15Gray}>{location}</Text>

        {payment && (
          <View
            style={[
              styles.horizontalWrapper,
              { marginTop: Metrics.baseMargin }
            ]}
          >
            <Text style={[ApplicationStyles.sB16Black, { flex: 1 }]}>{`${
              payment.id
            } | $${payment.amount} | ${payment.method}`}</Text>
            <Image
              source={Images.next}
              resizeMode="contain"
              style={styles.icon}
            />
          </View>
        )}
      </ButtonView>
    );
  }
}

export default OrderItem;

const styles = {
  container: {
    backgroundColor: Colors.background.primary,
    paddingHorizontal: Metrics.baseMargin,
    paddingVertical: Metrics.baseMargin + Metrics.smallMargin
  },
  horizontalWrapper: {
    flexDirection: "row",
    alignItems: "center"
  },
  icon: {
    width: Metrics.icon.tiny * 1.5,
    height: Metrics.icon.tiny * 1.5
  }
};
