import { StyleSheet } from "react-native";
import { Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flexDirection: "row",
    height: Metrics.navBarHeight,
    alignItems: "center",
    justifyContent: "center"
  },
  iconContainer: {
    paddingHorizontal: Metrics.baseMargin,
    alignItems: "center",
    justifyContent: "center",
    height: Metrics.navBarHeight - Metrics.statusBarHeight
  },
  icon: {
    width: Metrics.ratio(17),
    height: Metrics.ratio(20)
  }
});
