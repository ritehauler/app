// @flow
import { StyleSheet, Platform } from "react-native";
import { Colors, Fonts, Metrics } from "../../theme";

export default StyleSheet.create({
  inputStyle: {
    color: Colors.text.secondary,
    fontFamily: Fonts.type.base,
    fontSize: Fonts.size.small,
    marginTop: 1
  },
  floatingStyle: {
    color: Colors.text.primary,
    fontFamily: Fonts.type.base,
    fontSize: Fonts.size.xxxSmall,
    left: Platform.OS === "ios" ? -4 : 0,
    marginTop: Platform.OS === "ios" ? -8 : 2
  },
  inputContainer: {
    borderWidth: 1,
    borderColor: Colors.border,
    backgroundColor: Colors.background.secondary,
    paddingVertical:
      Platform.OS === "ios" ? Metrics.baseMargin : Metrics.smallMargin,
    paddingHorizontal: Metrics.smallMargin,
    marginBottom: Metrics.smallMargin * 0.6,
    marginTop: Metrics.smallMargin * 0.6,
    flexDirection: "row",
    alignItems: "center"
  },
  rightText: {
    paddingHorizontal: Metrics.smallMargin,
    paddingVertical: Metrics.smallMargin * 1.2
  },
  rightImage: {
    marginHorizontal: Metrics.smallMargin
  }
});
