// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  extraItem: {
    marginTop: Metrics.baseMargin,
    marginBottom: Metrics.smallMargin * 1.5
  },
  extraCharge: {
    marginTop: Metrics.smallMargin * 1.25,
    lineHeight: Metrics.baseMargin * 1.125
  }
});
