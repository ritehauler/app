// @flow
import { StyleSheet } from "react-native";
import { Metrics } from "../../../theme";

export default StyleSheet.create({
  container: {
    paddingBottom: Metrics.baseMargin
  },
  info: {
    marginTop: Metrics.addItemInputSpacing,
    lineHeight: Metrics.baseMargin * 1.25
  }
});
