// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background.primary
  },
  box: {
    position: "absolute",
    backgroundColor: Colors.background.secondary,
    margin: Metrics.baseMargin,
    left: 0,
    right: 0
  }
});
