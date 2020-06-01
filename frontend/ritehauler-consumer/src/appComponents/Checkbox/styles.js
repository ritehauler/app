// @flow
import { StyleSheet } from "react-native";
import { Metrics } from "../../theme";

export default StyleSheet.create({
  checkbox: {
    paddingRight: Metrics.smallMargin,
    paddingBottom: Metrics.baseMargin,
    paddingTop: Metrics.smallMargin * 0.5
  },
  confirmText: {
    lineHeight: Metrics.baseMargin * 1.25,
    marginRight: Metrics.baseMargin
  }
});
