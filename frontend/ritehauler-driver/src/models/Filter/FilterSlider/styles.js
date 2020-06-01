// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  container: {
    marginHorizontal: Metrics.baseMargin,
    backgroundColor: Colors.background.secondary,
    padding: Metrics.baseMargin
  },
  valuesContainer: {
    flexDirection: "row",
    marginBottom: Metrics.baseMargin * 1.5
  },
  title: {
    flex: 1
  },
  multiSliderContainerStyle: {
    marginTop: 0,
    height: 0,
    marginBottom: Metrics.smallMargin,
    marginLeft: Metrics.smallMargin
  },
  multiSliderSelectedStyle: { backgroundColor: Colors.background.accent }
});
