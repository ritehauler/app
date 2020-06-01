// @flow
import React from "react";
import { Actions } from "react-native-router-flux";
import PropTypes from "prop-types";
import { Image } from "react-native";

import { Images } from "../../theme";
import { ButtonView } from "../../components";
import styles from "./styles";

export default class LeftViewNavigation extends React.PureComponent {
  static propTypes = {
    action: PropTypes.func,
    image: PropTypes.oneOfType([PropTypes.number, PropTypes.object])
  };

  static defaultProps = {
    action: () => Actions.pop(),
    image: Images.back
  };

  state = {
    action: this.props.action,
    image: this.props.image
  };

  componentWillReceiveProps(nextProps) {
    this.setState({ action: nextProps.action, image: nextProps.image });
  }

  setActionAndImage = (action, image) => {
    this.setState({ action, image });
  };

  render() {
    const { action, image } = this.state;
    return (
      <ButtonView
        onPress={action}
        style={styles.container}
        isBackgroundBorderLess
      >
        <Image source={image} resizeMode="contain" />
      </ButtonView>
    );
  }
}
