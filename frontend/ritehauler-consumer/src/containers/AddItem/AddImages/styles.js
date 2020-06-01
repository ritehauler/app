// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  container: { marginTop: Metrics.smallMargin * 1.2 },
  imagesContainer: {
    backgroundColor: Colors.background.secondary,
    paddingTop: Metrics.smallMargin * 1.25,
    paddingBottom: Metrics.baseMargin * 1,
    paddingLeft: Metrics.smallMargin * 0.9,
    paddingRight: Metrics.baseMargin
  },
  title: {
    marginTop: Metrics.smallMargin * 0.25,
    marginBottom: Metrics.smallMargin,
    marginLeft: Metrics.smallMargin * 0.9
  },
  rowContainer: {
    flexDirection: "row",
    marginLeft: Metrics.smallMargin * -0.5
  },
  imageContainer: {
    width: Metrics.uploadImage,
    height: Metrics.uploadImage,
    backgroundColor: Colors.background.addImage,
    alignItems: "center",
    justifyContent: "center",
    marginLeft: Metrics.smallMargin * 1.5
  },
  image: {
    width: Metrics.uploadImage,
    height: Metrics.uploadImage
  }
});
