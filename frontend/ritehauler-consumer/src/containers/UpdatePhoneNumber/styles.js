// @flow
import { StyleSheet } from "react-native";
import { Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    paddingHorizontal: Metrics.baseMargin,
    paddingBottom: Metrics.baseMargin
  },
  header: {
    paddingHorizontal: Metrics.baseMargin,
    lineHeight: Metrics.baseMargin * 1.6,
    paddingTop: Metrics.baseMargin * 2.5,
    paddingBottom: Metrics.baseMargin * 1.5
  },
  button: {
    marginTop: Metrics.baseMargin
  }
});
