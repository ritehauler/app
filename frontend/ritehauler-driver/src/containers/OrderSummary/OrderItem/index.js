// @flow
import React, { PureComponent } from "react";
import { Actions } from "react-native-router-flux";
import { View, Text, Image } from "react-native";
import Swipeout from "react-native-swipeout";
import PropTypes from "prop-types";
import { ButtonView } from "../../../components";
import { Metrics, Colors, ApplicationStyles, Images } from "../../../theme";
import itemBox from "../../../reducers/itemBox";

const swipeoutBtns = (index, cbOnDelete) => [
  {
    text: "Delete",
    type: "delete",
    onPress: onPressDelete(index, cbOnDelete)
  }
];

const onPressDelete = (index, cbOnDelete) => event => {
  if (cbOnDelete) {
    cbOnDelete(index);
  }
};

const withSwipeOut = (
  title,
  dimensions,
  quantity,
  dollar,
  index,
  section,
  onPressItem,
  data,
  cbOnDelete
) => (
  <Swipeout key={title} right={swipeoutBtns(index, cbOnDelete)} autoClose>
    <ButtonView
      style={styles.container}
      onPress={() => {
        onPressItem(data, index, section);
      }}
    >
      <View style={{ flex: 1 }}>
        <View
          style={{
            flexDirection: "row",
            alignItems: "center",
            justifyContent: "flex-start"
          }}
        >
          <Text style={ApplicationStyles.sB16Black}>{title}</Text>
          {dollar && <Image source={Images.dollar} style={styles.dollar} />}
        </View>
        {dimensions && (
          <Text
            style={[
              ApplicationStyles.re13Black,
              { paddingTop: Metrics.smallMargin * 0.5 }
            ]}
          >
            {dimensions}
          </Text>
        )}
      </View>
      <Text style={ApplicationStyles.re13Gray}>x{quantity}</Text>
    </ButtonView>
  </Swipeout>
);

const withOutSwipeOut = (
  title,
  dimensions,
  quantity,
  dollar,
  index,
  section,
  onPressItem,
  data,
  perExtraItemCharge,
  isExtraItem
) => {
  return (
    <View key={title} style={styles.container}>
      <View style={{ flex: 1 }}>
        <View
          style={{
            flexDirection: "row",
            alignItems: "center",
            justifyContent: "flex-start"
          }}
        >
          <Text style={ApplicationStyles.sB16Black}>{title}</Text>
          {dollar && <Image source={Images.dollar} style={styles.dollar} />}
        </View>
        {dimensions && (
          <Text
            style={[
              ApplicationStyles.re13Black,
              { paddingTop: Metrics.smallMargin * 0.5 }
            ]}
          >
            {dimensions}
          </Text>
        )}
        {isExtraItem && (
          <Text
            style={[
              ApplicationStyles.dLight14Orange,
              styles.extraItemDescription
            ]}
          >{`$${perExtraItemCharge} will be charged on above item`}</Text>
        )}
      </View>
      <Text style={ApplicationStyles.re13Gray}>x{quantity}</Text>
    </View>
  );
};

const OrderItem = ({
  title,
  dimensions,
  quantity,
  dollar,
  index,
  showSwipeOut,
  section,
  onPressItem,
  data,
  cbOnDelete,
  perExtraItemCharge,
  isExtraItem
}) => {
  return showSwipeOut
    ? withSwipeOut(
        title,
        dimensions,
        quantity,
        dollar,
        index,
        section,
        onPressItem,
        data,
        cbOnDelete
      )
    : withOutSwipeOut(
        title,
        dimensions,
        quantity,
        dollar,
        index,
        section,
        onPressItem,
        data,
        perExtraItemCharge,
        isExtraItem
      );
};

const styles = {
  container: {
    flexDirection: "row",
    padding: Metrics.baseMargin,
    alignItems: "center",
    justifyContent: "center",
    flex: 1,
    backgroundColor: Colors.background.primary
  },
  dollar: {
    marginTop: Metrics.ratio(0.5),
    marginLeft: Metrics.smallMargin,
    width: Metrics.icon.normal / 1.1,
    height: Metrics.icon.normal / 1.1,
    resizeMode: "contain"
  },
  extraItemDescription: {
    marginTop: Metrics.ratio(5)
  }
};

export default OrderItem;
