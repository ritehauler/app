// @flow
import { StyleSheet } from "react-native";
import { Metrics, Colors } from "../../theme";

export default StyleSheet.create({
  linearGradient: {
    height: Metrics.buttonUIHeight,
    justifyContent: "center",
    alignItems: "center",
    elevation: 1
  },
  bottom: {
    marginBottom: Metrics.isIphoneX ? Metrics.bottomSpaceIphoneX : 0
  },
  disableView: {
    height: Metrics.buttonUIHeight,
    justifyContent: "center",
    alignItems: "center",
    elevation: 1,
    backgroundColor: Colors.background.disableButton
  }
});
