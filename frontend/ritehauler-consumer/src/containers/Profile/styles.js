// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background.primary
  },
  headerSpace: {
    height: Metrics.baseMargin * 6
  },
  imageContainer: {
    position: "absolute",
    left: Metrics.screenWidth / 2 - Metrics.profileImage / 2,
    top: 35,
    width: Metrics.profileImage,
    height: Metrics.profileImage
  },
  image: {
    width: Metrics.profileImage,
    height: Metrics.profileImage
  },
  formContainer: {
    paddingTop: Metrics.baseMargin * 5.5,
    paddingHorizontal: Metrics.baseMargin,
    paddingBottom: Metrics.baseMargin
  },
  scroll: {
    flex: 1
  },
  firstName: {
    flex: 1
  },
  lastName: {
    flex: 1,
    marginLeft: Metrics.inputSpacing * 2
  },
  name: {
    flexDirection: "row"
  },
  disable: {
    opacity: 0.5
  },
  customImagePlaceholderDefaultStyle: {
    width: Metrics.profileImage,
    height: Metrics.profileImage
  }
});
