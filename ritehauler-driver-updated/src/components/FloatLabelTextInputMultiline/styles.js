// @flow
import { StyleSheet } from "react-native";
import { Colors, Fonts, Metrics } from "../../theme";

export default StyleSheet.create({
  inputStyle: {
    color: Colors.text.primary,
    fontFamily: Fonts.type.base,
    fontSize: Fonts.size.xSmall,
    marginTop: Metrics.ratio(1)
  },
  floatingStyle: {
    color: Colors.text.primary,
    fontFamily: Fonts.type.medium,
    fontSize: Fonts.size.xxxSmall,
    marginTop: Metrics.ratio(2)
  },
  inputContainer: {
    borderWidth: 1 * Metrics.ratio,
    borderColor: Colors.border,
    backgroundColor: Colors.background.primary,
    paddingVertical: Metrics.smallMargin,
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
