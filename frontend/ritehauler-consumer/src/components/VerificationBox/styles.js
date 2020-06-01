// @flow
import { StyleSheet } from "react-native";

import { Colors, Fonts, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flexDirection: "row",
    justifyContent: "center",
    width: Metrics.screenWidth,
    marginRight: Metrics.baseMargin * 1.5
  },
  input: {
    height: Metrics.doubleBaseMargin * 1.875,
    width: Metrics.doubleBaseMargin * 1.875,
    backgroundColor: Colors.background.verificationBox,
    marginLeft: Metrics.smallMargin,
    fontSize: Fonts.size.xxxLarge,
    textDecorationLine: "none",
    textAlign: "center",
    textAlignVertical: "center",
    fontFamily: Fonts.type.light
  }
});
