// @flow
import React from "react";
import PropTypes from "prop-types";
import { Image, View, TextInput } from "react-native";

import { Images, Colors, ApplicationStyles } from "../../theme";
import { ButtonView, Text } from "../../components";
import styles from "./styles";

export default class SearchBar extends React.PureComponent {
  static propTypes = {
    searchText: PropTypes.string,
    hasRightView: PropTypes.bool,
    rightText: PropTypes.string,
    rightTextColor: PropTypes.string,
    rightImage: PropTypes.number,
    onRightViewPress: PropTypes.func,
    onSearchTextChange: PropTypes.func,
    placeHolder: PropTypes.string
  };

  static defaultProps = {
    searchText: "",
    hasRightView: false,
    rightText: "",
    rightTextColor: "primary",
    rightImage: Images.arrowDown,
    onRightViewPress: () => {},
    onSearchTextChange: () => {},
    placeHolder: ""
  };

  constructor(props) {
    super(props);
    this.onChangeSearchText = this.onChangeSearchText.bind(this);
  }

  state = {
    rightText: this.props.rightText,
    rightImage: this.props.rightImage,
    searchText: this.props.searchText,
    rightTextColor: this.props.rightTextColor,
    placeHolder: this.props.placeHolder
  };

  onChangeSearchText = text => {
    const { onSearchTextChange } = this.props;
    this.setState({ searchText: text });
    onSearchTextChange(text);
  };

  setPlaceholder = placeHolder => {
    this.setState({ placeHolder });
  };

  setSearchText = searchText => {
    this.setState({ searchText });
  };

  setRightText = rightText => {
    this.setState({ rightText });
  };

  setRightTextColor = rightTextColor => {
    this.setState({ rightTextColor });
  };

  setRightTextAndImage = (rightText, rightImage) => {
    this.setState({ rightText, rightImage });
  };

  getText() {
    return this.state.searchText;
  }

  setValues = (
    searchText,
    rightText,
    rightImage,
    rightTextColor,
    placeHolder
  ) => {
    this.setState({
      searchText,
      rightText,
      rightImage,
      rightTextColor,
      placeHolder
    });
  };

  _renderLeftView() {
    const { searchText, placeHolder } = this.state;
    return (
      <View style={styles.leftView}>
        <Image source={Images.search} />
        <TextInput
          autoCorrect={false}
          underlineColorAndroid="transparent"
          placeholder={placeHolder}
          style={[styles.textInput, ApplicationStyles.re16Black]}
          placeholderTextColor={Colors.text.quaternary}
          value={searchText}
          returnKeyType="done"
          onChangeText={this.onChangeSearchText}
        />
        {searchText !== "" && (
          <ButtonView onPress={() => this.onChangeSearchText("")}>
            <Image source={Images.crossSearch} style={styles.cross} />
          </ButtonView>
        )}
      </View>
    );
  }

  _renderSeparator() {
    return <View style={styles.separator} />;
  }

  _renderRightView() {
    const { rightText, rightImage, rightTextColor } = this.state;
    const { onRightViewPress } = this.props;
    return (
      <ButtonView
        style={styles.rightView}
        onPress={onRightViewPress}
        enableClick
      >
        <Text style={[styles.rightText, ApplicationStyles.re16Black]}>
          {rightText}
        </Text>
        <Image source={rightImage} />
      </ButtonView>
    );
  }

  render() {
    const { hasRightView } = this.props;
    return (
      <View style={styles.container}>
        {this._renderLeftView()}
        {hasRightView && this._renderSeparator()}
        {hasRightView && this._renderRightView()}
      </View>
    );
  }
}
