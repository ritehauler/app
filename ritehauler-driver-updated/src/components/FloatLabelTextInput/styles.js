// @flow
import { StyleSheet, Platform } from "react-native";
import { Colors, Fonts, Metrics } from "../../theme";

export default StyleSheet.create({
  inputStyle: {
    color: Colors.text.primary,
    fontFamily: Fonts.type.base,
    fontSize: Fonts.size.xSmall
  },
  floatingStyle: {
    color: Colors.text.quaternary,
    fontFamily: Fonts.type.medium,
    fontSize: Fonts.size.xxxSmall,
    marginTop: Metrics.smallMargin * 0.5
  },
  inputContainer: {
    borderWidth: 1,
    borderColor: Colors.border,
    height: Metrics.ratio(60),
    backgroundColor: Colors.background.primary,
    paddingVertical: Metrics.smallMargin,
    paddingHorizontal: Metrics.smallMargin,
    marginBottom: Metrics.smallMargin * 0.7,
    marginTop: Metrics.smallMargin * 0.7,
    flexDirection: "row",
    alignItems: "center"
  },
  rightText: {
    paddingHorizontal:
      Platform.OS === "ios" ? Metrics.smallMargin * 0.6 : Metrics.smallMargin,
    paddingVertical: Metrics.smallMargin * 1.2
  },
  rightImage: {
    marginHorizontal: Metrics.smallMargin
  }
});
