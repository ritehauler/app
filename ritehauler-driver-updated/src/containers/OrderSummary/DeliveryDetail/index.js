// @flow
import React, { PureComponent } from "react";
import { Actions } from "react-native-router-flux";
import { View, Text, Image } from "react-native";

import PropTypes from "prop-types";
import { Metrics, Colors, ApplicationStyles, Images } from "../../../theme";

const DeliveryDetail = ({ proffesionals, loadingPrice }) => (
  <View style={styles.container}>
    <View style={{ flex: 1 }}>
      <Text style={ApplicationStyles.sB16Black}>Delivery Proffesional</Text>
      <Text style={ApplicationStyles.lBlack16}>{proffesionals}</Text>
    </View>
    <View>
      <Text style={ApplicationStyles.sB16Black}>Loading Price</Text>
      <Text style={ApplicationStyles.lBlack16}>{`$${loadingPrice}`}</Text>
    </View>
  </View>
);

const styles = {
  container: {
    flexDirection: "row",
    padding: Metrics.baseMargin,
    flex: 1,
    backgroundColor: Colors.background.primary
  }
};

export default DeliveryDetail;
