// @flow
import { StyleSheet } from "react-native";
import { Colors, Fonts, Metrics } from "../../../theme";

export default StyleSheet.create({
  textInput: {
    fontSize: Fonts.size.small,
    fontFamily: Fonts.type.light,
    color: Colors.text.primary,
    paddingHorizontal: Metrics.baseMargin,
    paddingTop: Metrics.baseMargin,
    paddingBottom: Metrics.baseMargin,
    backgroundColor: Colors.background.secondary
  }
});
