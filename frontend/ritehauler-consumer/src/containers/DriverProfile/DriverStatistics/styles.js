// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  container: {
    marginHorizontal: Metrics.baseMargin,
    marginVertical: Metrics.baseMargin * 1.25,
    paddingTop: Metrics.baseMargin * 1.5,
    paddingBottom: Metrics.baseMargin * 1.2,
    backgroundColor: Colors.background.secondary
  },
  title: {
    marginBottom: Metrics.baseMargin,
    paddingHorizontal: Metrics.baseMargin
  },
  list: {
    marginTop: Metrics.baseMargin,
    paddingLeft: Metrics.smallMargin * 1.5
  },
  separator: {
    marginHorizontal: Metrics.baseMargin
  }
});
