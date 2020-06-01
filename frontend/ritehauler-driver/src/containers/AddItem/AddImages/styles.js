// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics, Fonts } from "../../../theme";

export default StyleSheet.create({
  container: {
    backgroundColor: Colors.background.primary,
    paddingTop: Metrics.smallMargin * 1.25,
    //paddingBottom: Metrics.smallMargin,
    //paddingLeft: Metrics.smallMargin * 0.9,
    //paddingRight: Metrics.baseMargin,
    marginVertical: Metrics.smallMargin * 1.5
  },
  title: {
    marginBottom: Metrics.smallMargin * 0.75,
    marginLeft: Metrics.baseMargin * 0.9,
    color: Colors.text.quaternary,
    fontFamily: Fonts.type.medium,
    fontSize: Fonts.size.xxxSmall
  },
  rowContainer: { flexDirection: "row", marginLeft: Metrics.smallMargin * 0.7 },
  imageContainer: {
    width: Metrics.uploadImage,
    height: Metrics.uploadImage,
    backgroundColor: Colors.uploadImage,
    alignItems: "center",
    justifyContent: "center",
    marginLeft: Metrics.smallMargin
  },
  image: {
    width: Metrics.uploadImage,
    height: Metrics.uploadImage
  },
  cross: {
    position: "absolute"
  }
});
