// @flow
import { StyleSheet } from "react-native";
import { Metrics } from "../../../theme";

export default StyleSheet.create({
  list: {
    marginHorizontal: Metrics.baseMargin,
    marginTop: Metrics.baseMargin,
    width: Metrics.screenWidth - Metrics.baseMargin * 2
  }
});
