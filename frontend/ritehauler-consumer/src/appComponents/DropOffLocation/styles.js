// @flow
import { StyleSheet, Platform } from "react-native";
import { Metrics, Colors } from "../../theme";

export default StyleSheet.create({
  container: {
    flexDirection: "row",
    paddingHorizontal: Metrics.baseMargin,
    paddingBottom: Metrics.smallMargin * 1.5
  },
  imageContainer: {
    flexDirection: "column"
  },
  image: {
    marginRight: Metrics.smallMargin * 1.5,
    width: Metrics.baseMargin * 1.5,
    height: Metrics.baseMargin * 1.5
  },
  line: {
    width: Metrics.smallMargin / 4,
    backgroundColor: Colors.background.border,
    height:
      Platform.OS === "ios"
        ? Metrics.smallMargin * 1.2
        : Metrics.smallMargin * 1.8,
    marginLeft: Metrics.smallMargin * 1.4
  },
  contentContainer: {
    flexDirection: "column",
    flex: 1,
    marginTop: Metrics.smallMargin / 2
  },
  textDropOff: {
    marginTop: Metrics.smallMargin / 3
  },
  where: {
    marginTop: Metrics.smallMargin * 1.7
  }
});
