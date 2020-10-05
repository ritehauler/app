// @flow
import React, { PureComponent } from "react";
import { Actions } from "react-native-router-flux";
import { View, Text, Image } from "react-native";

import PropTypes from "prop-types";
import { Metrics, Colors, ApplicationStyles, Images } from "../../../theme";
import { Separator } from "../../../components";

const item = (title, amount, styles) => {
  return (
    <View style={styles}>
      <View>
        <Text style={ApplicationStyles.sB16Black}>{title}</Text>
        <Text textAlign="left" style={ApplicationStyles.lBlack16}>
          ${amount}
        </Text>
      </View>
    </View>
  );
};

const fairDetails = (baseFee, perMin, minimum) => (
  <View
    style={{
      flexDirection: "row",
      flex: 1,
      justifyContent: "space-around"
    }}
  >
    {item("Base Fee", baseFee, { flex: 0.8 })}
    {item("Per Min", perMin, { flex: 1 })}
  </View>
);

const truckDetail = truck => (
  <View style={{ flex: 1, flexDirection: "row" }}>
    <Text style={[ApplicationStyles.tBold16, { flex: 1 }]}>Truck</Text>
    <Text style={ApplicationStyles.lBlack14}>{truck}</Text>
  </View>
);

const separator = () => (
  <View
    style={{
      paddingVertical: Metrics.smallMargin,
      flex: 1,
      alignItems: "center",
      flexDirection: "row"
    }}
  >
    <Separator />
  </View>
);

const coastEstimate = (val1, val2) => (
  <Text style={ApplicationStyles.re16Orange}>
    Est. cost {`$${val1} - $${val2}`}{" "}
  </Text>
);

const TruckDetailCard = ({
  truck,
  baseFee,
  perMin,
  minimum,
  maxEstimatedCharges,
  minEstimatedCharges
}) => (
  <View style={styles.container}>
    {truckDetail(truck)}
    {separator()}
    {fairDetails(baseFee, perMin, minimum)}
    {separator()}
    {coastEstimate(minEstimatedCharges, maxEstimatedCharges)}
  </View>
);

const styles = {
  container: {
    padding: Metrics.baseMargin,
    backgroundColor: Colors.background.primary
  },
  dollar: {
    marginLeft: Metrics.smallMargin,
    width: Metrics.icon.small,
    height: Metrics.icon.small,
    resizeMode: "contain"
  }
};

export default TruckDetailCard;
