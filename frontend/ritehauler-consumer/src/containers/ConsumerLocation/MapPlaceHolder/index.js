import React from "react";
import { Image } from "react-native";
import * as Animatable from "react-native-animatable";
import PropTypes from "prop-types";

import { Images } from "../../../theme";
import styles from "./styles";

export default class MapPlaceHolder extends React.PureComponent {
  static propTypes = {
    hide: PropTypes.bool
  };

  static defaultProps = { hide: false };

  state = {
    hide: this.props.hide
  };

  fadeOut = () => {
    if (!this.state.hide) {
      this.mapViewPlaceholder
        .fadeOut(1000)
        .then(endState => this.setState({ hide: true }));
    }
  };

  _renderContent() {
    return (
      <Animatable.View
        ref={ref => {
          this.mapViewPlaceholder = ref;
        }}
        style={styles.container}
      >
        <Image source={Images.mapPlaceholder} style={styles.image} />
      </Animatable.View>
    );
  }

  render() {
    const { hide } = this.state;
    if (hide) {
      return null;
    }
    return this._renderContent();
  }
}
