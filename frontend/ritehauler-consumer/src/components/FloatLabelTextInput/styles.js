// @flow
import { StyleSheet, Platform } from "react-native";
import { Colors, Fonts, Metrics } from "../../theme";

export default StyleSheet.create({
  inputStyle: {
    color: Colors.text.primary,
    fontFamily: Fonts.type.base,
    fontSize: Fonts.size.small
  },
  floatingStyle: {
    color: Colors.text.secondary,
    fontFamily: Fonts.type.base,
    fontSize: Fonts.size.xxxSmall,
    marginTop: Metrics.smallMargin * 0.5
  },
  inputContainer: {
    height: Metrics.doubleBaseMargin * 1.85,
    backgroundColor: Colors.background.secondary,
    paddingVertical: Metrics.smallMargin,
    paddingHorizontal: Metrics.smallMargin,
    marginVertical: Metrics.inputSpacing,
    flexDirection: "row",
    alignItems: "center",
    borderWidth: 1,
    borderColor: Colors.background.border
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
