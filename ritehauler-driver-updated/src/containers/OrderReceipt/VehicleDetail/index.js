// @flow
import React, { PureComponent } from "react";
import { Actions } from "react-native-router-flux";
import { View, Text, Image } from "react-native";

import PropTypes from "prop-types";
import { Metrics, Colors, ApplicationStyles, Images } from "../../../theme";
import { Separator, ImageView } from "../../../components";
import ImageLoad from "react-native-image-placeholder";

const item = (title, detail, styles) => {
  return (
    <View style={{ flexDirection: "row", flex: 1 }}>
      <View>
        <Text style={ApplicationStyles.sB16Black}>{title}</Text>
        <Text textAlign="left" style={ApplicationStyles.re13Gray}>
          {detail}
        </Text>
      </View>
    </View>
  );
};

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

const TruckDetailCard = ({
  duration,
  distance,
  driverName,
  vehicle,
  noPlate,
  driverImage
}) => {
  return (
    <View style={styles.container}>
      <View
        style={{
          flexDirection: "row",
          flex: 1,
          paddingVertical: Metrics.smallMargin / 2
        }}
      >
        {item("Duration", duration)}
        {item("Distance", distance)}
      </View>
      {separator()}
      <View
        style={{
          paddingVertical: Metrics.smallMargin / 2,
          flexDirection: "row",
          alignItems: "center"
        }}
      >
        <ImageView
          style={[
            styles.circleImageStyle,
            {
              marginRight: Metrics.ratio(10)
            }
          ]}
          isShowActivity={false}
          customImagePlaceholderDefaultStyle={styles.circleImageStyle}
          source={{ uri: driverImage }}
          borderRadius={Metrics.ratio(18)}
          placeholderSource={Images.personPlaceholder}
        />
        {item("Driver Name", driverName)}
      </View>
      {separator()}
      <View
        style={{
          flexDirection: "row",
          flex: 1,
          paddingVertical: Metrics.smallMargin / 2
        }}
      >
        {item("Vehicle", vehicle)}
        {item("Plate", noPlate)}
      </View>
    </View>
  );
};

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
  },
  circleImageStyle: {
    height: Metrics.ratio(36),
    width: Metrics.ratio(36),
    borderRadius: Metrics.ratio(18),
    backgroundColor: "white"
  }
};

export default TruckDetailCard;
