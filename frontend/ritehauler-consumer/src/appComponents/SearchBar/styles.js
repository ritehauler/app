// @flow
import { StyleSheet } from "react-native";
import { Metrics, Colors, Fonts } from "../../theme";

export default StyleSheet.create({
  container: {
    flexDirection: "row",
    backgroundColor: Colors.background.secondary,
    borderBottomColor: Colors.background.separator,
    borderBottomWidth: 1
  },
  leftView: {
    flexDirection: "row",
    flex: 1,
    alignItems: "center",
    marginLeft: Metrics.baseMargin
  },
  crossContainer: {
    paddingVertical: Metrics.smallMargin * 1.75,
    paddingHorizontal: Metrics.baseMargin
  },
  textInput: {
    paddingLeft: Metrics.smallMargin * 1.25,
    flex: 1,
    fontSize: Fonts.size.small,
    fontFamily: Fonts.type.base,
    color: Colors.text.primary,
    paddingBottom: Metrics.smallMargin * 1.75,
    paddingTop: Metrics.smallMargin * 1.75
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
