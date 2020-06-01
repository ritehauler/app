// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background.primary
  },
  scroll: {
    flex: 1,
    padding: Metrics.baseMargin
  },
  buttonView: {
    flexDirection: "row",
    alignItems: "center"
  },
  estimateCost: {
    flex: 0.6
  },
  midLine: {
    backgroundColor: "white",
    width: 0.5,
    height: Metrics.baseMargin * 1.5
  },
  confirm: {
    flex: 0.4,
    paddingVertical: Metrics.smallMargin * 1.5
  }
});
