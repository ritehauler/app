// @flow
import { StyleSheet } from "react-native";
import { Metrics, Colors, Fonts } from "../../theme";

export default StyleSheet.create({
  container: {
    flexDirection: "row",
    backgroundColor: Colors.background.primary
  },
  leftView: {
    flexDirection: "row",
    paddingVertical: Metrics.smallMargin * 1.75,
    paddingHorizontal: Metrics.baseMargin,
    flex: 1,
    alignItems: "center"
  },
  textInput: {
    paddingLeft: Metrics.smallMargin * 1.25,
    flex: 1,
    paddingBottom: 0,
    paddingTop: 0,
    paddingRight: Metrics.baseMargin
  },
  cross: {
    width: Metrics.baseMargin,
    height: Metrics.baseMargin
  },
  separator: {
    width: 1,
    top: 0,
    bottom: 0,
    marginVertical: Metrics.smallMargin * 1.25,
    backgroundColor: "#efefef"
  },
  rightView: {
    flexDirection: "row",
    paddingRight: Metrics.baseMargin,
    paddingVertical: Metrics.smallMargin * 1.75,
    paddingLeft: Metrics.baseMargin,
    alignItems: "center"
  },
  rightText: {
    marginRight: Metrics.baseMargin
  }
});
