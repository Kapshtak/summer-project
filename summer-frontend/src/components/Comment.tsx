import React, { useContext, useState } from 'react'
import { CommentType } from '../types/events'
import { CurrentUserContext } from '../context/context'
import { hideComment, updateComment } from '../api/EventsAPI'
import cl from './Comment.module.css'

interface CommentProps {
  comment: CommentType
  event?: number | string
  fetch?: () => void
}

const Comment: React.FC<CommentProps> = ({ comment, event, fetch }) => {
  const currentUser = useContext(CurrentUserContext).currentUser
  const [commentText, setCommentText] = useState(comment.text)
  const [isEdit, setIsEdit] = useState(true)

  const handleUpdate = async (e: React.FormEvent) => {
    e.preventDefault()
    setIsEdit(!isEdit)
    try {
      if (currentUser && event) {
        const response = await updateComment(comment.id, commentText)
        if ('message' in response) {
          console.error(response.message)
        } else {
          if (fetch) {
            fetch()
          }
        }
      }
    } catch (e) {
      console.error(e)
    }
  }

  const handleHide = async (e: React.FormEvent) => {
    e.preventDefault()
    try {
      if (currentUser && event) {
        const response = await hideComment(comment.id)
        if ('message' in response) {
          console.error(response.message)
        } else {
          if (fetch) {
            fetch()
          }
        }
      }
    } catch (e) {
      console.error(e)
    }
  }

  const toggle = () => {
    setIsEdit(!isEdit)
  }

  const handleOnChange = (e: any) => {
    e.preventDefault()
    setCommentText(e.target.value)
  }

  return (
    <div>
      {isEdit && (
        <div className={cl.comment_container}>
          <h3 className={cl.comment_author}>
            {comment.author.email}{' '}
            {comment.author.roles.includes('ROLE_ADMIN') ? '👑' : ''}
          </h3>
          <p className={cl.comment_text}>{comment.text}</p>
          {currentUser && comment.author.id === currentUser.user && (
            <button className={cl.comment_button} onClick={toggle}>
              <span
                className="material-symbols-outlined"
                style={{ verticalAlign: 'middle',fontSize:'18px' }}
              >
                edit
              </span>
            </button>
          )}
          {currentUser && currentUser.roles.includes('ROLE_ADMIN') && (
            <button className={cl.comment_button_hide} onClick={handleHide}>
              Hide
            </button>
          )}
        </div>
      )}

      {!isEdit && (
        <div className={cl.comment_container}>
          <h3 className={cl.comment_author}>
            {comment.author.email}{' '}
            {comment.author.roles.includes('ROLE_ADMIN') ? '👑' : ''}
          </h3>
          <textarea
            className={cl.comment_input}
            name="commentText"
            onChange={handleOnChange}
            value={commentText}
          />
          <button className={cl.comment_button} onClick={handleUpdate}>
            Save
          </button>
        </div>
      )}
    </div>
  )
}

export default Comment
