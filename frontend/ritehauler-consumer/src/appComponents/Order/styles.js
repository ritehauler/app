// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flexDirection: "row",
    elevation: 1,
    height: Metrics.buttonUIHeight,
    alignItems: "center",
    justifyContent: "center",
    marginTop: Metrics.smallMargin,
    backgroundColor: Colors.background.facebook
  },
  midLine: {
    width: 0.5,
    height: Metrics.smallMargin * 3.5,
    backgroundColor: Colors.background.secondary,
    marginHorizontal: Metrics.baseMargin * 1.25
  }
});
