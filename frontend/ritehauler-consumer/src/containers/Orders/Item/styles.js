// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  container: {
    backgroundColor: Colors.background.secondary,
    paddingVertical: Metrics.baseMargin * 1.5,
    paddingHorizontal: Metrics.baseMargin
  },
  dateAndStatusContainer: {
    flexDirection: "row",
    marginBottom: Metrics.smallMargin * 0.75
  },
  date: {
    flex: 1,
    marginRight: Metrics.baseMargin
  },
  address: {
    lineHeight: Metrics.baseMargin * 1.4
  },
  infoContainer: {
    flexDirection: "row",
    marginTop: Metrics.baseMargin
  },
  info: {
    flex: 1,
    marginRight: Metrics.baseMargin
  }
});
