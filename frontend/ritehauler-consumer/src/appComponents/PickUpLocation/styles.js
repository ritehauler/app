// @flow
import { StyleSheet, Platform } from "react-native";
import { Metrics, Colors } from "../../theme";

export default StyleSheet.create({
  container: {
    flexDirection: "row",
    paddingHorizontal: Metrics.baseMargin,
    paddingTop: Metrics.smallMargin
  },
  imageContainer: {
    flexDirection: "column",
    marginTop:
      Platform.OS === "ios" ? Metrics.smallMargin : Metrics.smallMargin * 1.3
  },
  image: {
    marginRight: Metrics.smallMargin * 1.5,
    width: Metrics.baseMargin * 1.5,
    height: Metrics.baseMargin * 1.5
  },
  line: {
    width: Metrics.smallMargin / 4,
    backgroundColor: Colors.background.border,
    flex: 1,
    marginLeft: Metrics.smallMargin * 1.4
  },
  contentContainer: {
    flexDirection: "column",
    flex: 1,
    paddingBottom: Metrics.smallMargin
  },
  textPickUp: {
    marginTop: Metrics.smallMargin / 3
  }
});
