import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  containerStyle: {
    flexDirection: "row",
    backgroundColor: Colors.background.primary
  },
  buttonStyle: {
    width: Metrics.statsNavigationImageWidth,
    padding: Metrics.baseMargin,
    alignItems: "center",
    justifyContent: "center"
  },
  titleContainerStyle: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
    padding: Metrics.baseMargin
  }
});
