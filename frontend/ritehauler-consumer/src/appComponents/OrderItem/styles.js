// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    backgroundColor: Colors.background.secondary
  },
  content: {
    flexDirection: "row",
    paddingHorizontal: Metrics.baseMargin,
    paddingVertical: Metrics.smallMargin * 1.25,
    alignItems: "center"
  },
  separator: {
    marginHorizontal: Metrics.baseMargin
  },
  info: {
    flexDirection: "column",
    flex: 1,
    marginRight: Metrics.baseMargin * 1.5
  },
  nameContainer: {
    marginBottom: Metrics.smallMargin * 0.5,
    flexDirection: "row",
    alignItems: "center"
  },
  expensive: {
    marginLeft: Metrics.smallMargin * 0.5
  },
  extraItem: {
    paddingHorizontal: Metrics.baseMargin,
    paddingBottom: Metrics.smallMargin * 1.25
  }
});
