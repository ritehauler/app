import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background.login,
    padding: Metrics.baseMargin
  },
  resetText: {
    textAlign: "center",
    lineHeight: Metrics.lineHeight,
    paddingRight: Metrics.baseMargin,
    paddingTop: Metrics.doubleBaseMargin,
    paddingBottom: Metrics.baseMargin
  },
  button: {
    marginBottom: Metrics.smallMargin,
    marginTop: Metrics.smallMargin
  }
});
