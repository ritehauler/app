// @flow
import { StyleSheet, Platform } from "react-native";
import { Colors, Metrics, Fonts } from "../../theme";

export default StyleSheet.create({
  container: {
    padding: Metrics.baseMargin,
    backgroundColor: Colors.background.secondary,
    marginHorizontal: Metrics.baseMargin,
    marginBottom: Metrics.baseMargin
  },
  title: {
    marginBottom: Metrics.baseMargin
  },
  textInput: {
    fontSize: Fonts.size.small,
    fontFamily: Fonts.type.light,
    color: Colors.text.primary,
    paddingBottom: 0,
    paddingTop:
      Platform.OS === "ios"
        ? Metrics.baseMargin * 0.9
        : Metrics.baseMargin * 0.6
  }
});
