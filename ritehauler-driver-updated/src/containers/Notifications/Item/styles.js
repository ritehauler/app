// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  container: {
    backgroundColor: Colors.background.primary,
    paddingHorizontal: Metrics.baseMargin,
    paddingBottom: Metrics.baseMargin,
    paddingTop: Metrics.smallMargin * 1.5,
    flexDirection: "row",
    marginHorizontal: Metrics.baseMargin
  },
  content: {
    flex: 1
  },
  text: {
    lineHeight: Metrics.baseMargin * 1.5
  },
  dateContainer: {
    flexDirection: "row",
    marginTop: Metrics.smallMargin * 1.5
  },
  date: {
    flex: 1
  },
  dot: {
    marginRight: Metrics.smallMargin,
    marginTop: Metrics.smallMargin
  }
});
