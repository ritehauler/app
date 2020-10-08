import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
    container: {
        padding: Metrics.smallMargin,
        backgroundColor: Colors.background.backgroundCircleView,
        width: Metrics.thumbImageWidth,
        height: Metrics.thumbImageHeight,
        borderRadius: Metrics.thumbImageRadius,
        alignItems: "center",
        justifyContent: "center"
    },
    textStyle: {
        textAlign: "center",
        backgroundColor: Colors.transparent
    }

});