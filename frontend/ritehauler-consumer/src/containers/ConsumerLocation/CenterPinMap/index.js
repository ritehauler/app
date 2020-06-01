// @flow
import * as Animatable from "react-native-animatable";
import * as Progress from "react-native-progress";
import { View, Image } from "react-native";
import PropTypes from "prop-types";
import React from "react";

import { Text, ButtonView } from "../../../components";
import { Images, Colors } from "../../../theme";
import styles from "./styles";

export default class CenterPinMap extends React.PureComponent {
  static propTypes = {
    image: PropTypes.number,
    text: PropTypes.string,
    isLoading: PropTypes.bool,
    disable: PropTypes.bool,
    onRetryPress: PropTypes.func,
    hideAnimatableView: PropTypes.bool
  };

  static defaultProps = {
    image: undefined,
    text: undefined,
    isLoading: true,
    disable: false,
    onRetryPress: undefined,
    hideAnimatableView: false
  };

  constructor(props) {
    super(props);
    this.empty = this.empty.bind(this);
  }

  empty() {}

  /* =====================  zoom in / zoom out functions ======================  */

  lastState = "";
  lastTransitionState = "";

  /*
  zoomInCenter = (time = 300) => {
    console.log("zoomInCenter start");
    if (this.animatableView) {
      this.animatableView.zoomIn(time).then(endState => {
        console.log("zoomInCenter end", endState);
      });
    }
  };

  zoomOutCenter = (time = 300) => {
    console.log("zoomOutCenter start");
    if (this.animatableView) {
      this.animatableView.zoomOut(time).then(endState => {
        console.log("zoomOutCenter end", endState);
      });
    }
  };
  */

  zoomInCenter = (time = 500) => {
    this.lastTransitionState = "zoomInStart";
    if (this.animatableView && this.lastState !== "zoomOutStart") {
      this.lastState = "zoomInStart";
      this.animatableView.zoomIn(time).then(endState => {
        this.lastState = "zoomInEnd";
        if (this.lastTransitionState === "zoomOutStart") {
          this.zoomOutCenter();
        }
      });
    }
  };

  zoomOutCenter = (time = 500) => {
    this.lastTransitionState = "zoomOutStart";
    if (this.animatableView && this.lastState !== "zoomInStart") {
      this.lastState = "zoomOutStart";
      this.animatableView.zoomOut(time).then(endState => {
        this.lastState = "zoomOutEnd";
        if (this.lastTransitionState === "zoomInStart") {
          this.zoomInCenter();
        }
      });
    }
  };

  /* =====================  render functions ======================  */

  _renderImage() {
    const { image } = this.props;
    return <Image source={image} style={styles.image} />;
  }

  _renderRetryImage() {
    return <Image style={styles.retryIcon} source={Images.retryUpload} />;
  }

  _renderText() {
    const { text, onRetryPress } = this.props;
    return (
      <ButtonView
        style={styles.textContainer}
        onPress={onRetryPress || this.empty}
        disableRipple={!onRetryPress}
      >
        <Text size="xxSmall">{text}</Text>
        {onRetryPress && this._renderRetryImage()}
      </ButtonView>
    );
  }

  _renderLoader() {
    return (
      <View style={styles.loaderView}>
        <Progress.CircleSnail
          color={[Colors.background.secondary]}
          size={38}
          duration={1000}
          spinDuration={1000}
          hidesWhenStopped={false}
          style={styles.progressBar}
        />
      </View>
    );
  }

  _renderConnectLine() {
    return <View style={styles.connectLine} />;
  }

  _renderPinCircle() {
    return <View style={styles.pinCircle} />;
  }

  _renderAnimatableView() {
    const { isLoading, image, text } = this.props;
    return (
      <Animatable.View
        ref={ref => {
          this.animatableView = ref;
        }}
        style={styles.animatableView}
      >
        {isLoading && this._renderLoader()}
        {image && this._renderImage()}
        {text && this._renderText()}
        {this._renderConnectLine()}
      </Animatable.View>
    );
  }

  render() {
    const { disable, hideAnimatableView } = this.props;
    if (disable) {
      return null;
    }
    return (
      <View style={styles.container} pointerEvents="box-none">
        {!hideAnimatableView && this._renderAnimatableView()}
        {this._renderPinCircle()}
      </View>
    );
  }
}
