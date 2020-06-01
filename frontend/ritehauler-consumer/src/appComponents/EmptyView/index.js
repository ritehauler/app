// @flow
import React from "react";
import { View, Image } from "react-native";
import PropTypes from "prop-types";

import { Text, ButtonView } from "../../components";
import { Images } from "../../theme";
import styles from "./styles";

export default class EmptyView extends React.PureComponent {
  static propTypes = {
    image: PropTypes.number,
    title: PropTypes.string.isRequired,
    description: PropTypes.string,
    buttonText: PropTypes.string,
    onPressButton: PropTypes.func
  };

  static defaultProps = {
    image: Images.noInternet,
    description: "",
    buttonText: "",
    onPressButton: () => {}
  };

  _renderImage() {
    const { image } = this.props;
    return <Image source={image} />;
  }

  _renderTitle() {
    const { title } = this.props;
    return (
      <Text size="large" type="bold" style={styles.title}>
        {title}
      </Text>
    );
  }

  _renderDescription() {
    const { description } = this.props;
    return (
      <Text size="small" color="secondary" style={styles.description}>
        {description}
      </Text>
    );
  }

  _renderButton() {
    const { buttonText, onPressButton } = this.props;
    if (buttonText === "") {
      return null;
    }

    return (
      <ButtonView style={styles.buttonView} onPress={onPressButton}>
        <Text color="accent" type="bold" size="large">
          {buttonText}
        </Text>
      </ButtonView>
    );
  }

  render() {
    return (
      <View style={styles.container}>
        {this._renderImage()}
        {this._renderTitle()}
        {this._renderDescription()}
        {this._renderButton()}
      </View>
    );
  }
}
